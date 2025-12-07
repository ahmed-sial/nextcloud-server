describe("TC-FS-11", () => {
    it("TC-FS-11", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Create public link").click();

        cy.contains("label", "Password protect").click();

        const longPassword = "a".repeat(101);
        cy.get('input[type="password"]').type(longPassword);

        cy.get('input[type="password"]').blur();

        cy.contains("Password cannot exceed").should("be.visible");
    });
});