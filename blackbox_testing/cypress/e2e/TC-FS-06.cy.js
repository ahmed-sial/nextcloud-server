describe("TC-FS-06", () => {
    it("TC-FS-06", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Create public link").click();

        cy.contains("label", "Password protect").click();

        cy.get('input[type="password"]').type("123");

        cy.get('input[type="password"]').blur();

        cy.contains("Password must be at least").should("be.visible");
    });
});