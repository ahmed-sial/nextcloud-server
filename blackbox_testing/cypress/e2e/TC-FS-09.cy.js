describe("TC-FS-09", () => {
    it("TC-FS-09", () => {
        cy.login();
        cy.viewport(1089, 825);
        cy.visit("/apps/files/files");

        cy.get(".files-fileList", { timeout: 10000 }).should("be.visible");
        cy.get(".action-share").first().click();

        cy.contains("button", "Next").click();
        cy.contains("Please select a share type").should("be.visible");
    });
});